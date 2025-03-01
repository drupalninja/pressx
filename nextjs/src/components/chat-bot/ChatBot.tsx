'use client';

import { useState, useRef, useEffect } from 'react';
import ReactMarkdown from 'react-markdown';
import remarkGfm from 'remark-gfm';
import { User, Bot, Copy, Check } from 'lucide-react';

type Message = {
  content: string;
  role: 'user' | 'assistant';
  timestamp: number;
  links?: Array<{ text: string; url: string }>;
};

export default function ChatBot() {
  // Check for preview mode using the environment variable
  // This matches how preview mode is detected in the rest of the application
  const isPreviewMode = process.env.NEXT_PUBLIC_PREVIEW_MODE === 'true';

  // Don't render anything if not in preview mode
  if (!isPreviewMode) {
    return null;
  }

  const [isOpen, setIsOpen] = useState(false);
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [copiedCode, setCopiedCode] = useState<string | null>(null);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  // Add welcome message when chat is opened
  useEffect(() => {
    if (isOpen && messages.length === 0) {
      setMessages([
        {
          content: "👋 Hi! I'm the PressX Assistant.  I am here to help you create content for your PressX website.",
          role: 'assistant',
          timestamp: Date.now()
        }
      ]);
    }
  }, [isOpen, messages.length]);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  // Reset copied state after 2 seconds
  useEffect(() => {
    if (copiedCode) {
      const timeout = setTimeout(() => {
        setCopiedCode(null);
      }, 2000);
      return () => clearTimeout(timeout);
    }
  }, [copiedCode]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!input.trim() || isLoading) return;

    const userMessage: Message = {
      content: input.trim(),
      role: 'user',
      timestamp: Date.now(),
    };

    setMessages((prev) => [...prev, userMessage]);
    setInput('');
    setIsLoading(true);

    try {
      const response = await fetch('/api/chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          messages: [
            {
              role: 'user',
              content: userMessage.content
            }
          ]
        }),
      });

      if (!response.ok) {
        throw new Error('Failed to get response');
      }

      const data = await response.json();
      const assistantMessage: Message = {
        content: data.response.content,
        role: 'assistant',
        timestamp: Date.now(),
        links: data.links,
      };

      setMessages((prev) => [...prev, assistantMessage]);
    } catch (error) {
      console.error('Chat error:', error);
      const errorMessage: Message = {
        content: 'Sorry, I encountered an error. Please try again.',
        role: 'assistant',
        timestamp: Date.now(),
      };
      setMessages((prev) => [...prev, errorMessage]);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="fixed bottom-5 right-5 z-50">
      {!isOpen ? (
        <button
          onClick={() => setIsOpen(true)}
          className="bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-full font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2"
          aria-label="Open chat"
        >
          <Bot className="w-5 h-5" />
          <span>PressX ChatBot</span>
        </button>
      ) : (
        <div className="w-[450px] bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col min-h-[400px] max-h-[600px] border border-gray-200">
          <div className="bg-gray-100 p-4 flex justify-between items-center border-b border-gray-200">
            <div className="flex items-center gap-2">
              <Bot className="w-5 h-5 text-primary" />
              <h3 className="text-lg font-semibold text-gray-800">PressX ChatBot</h3>
            </div>
            <button
              onClick={() => setIsOpen(false)}
              className="text-gray-500 hover:text-gray-700 p-1 hover:bg-gray-200 rounded transition-colors duration-200"
              aria-label="Close chat"
            >
              <span className="text-xl">✕</span>
            </button>
          </div>
          <div className="flex-1 overflow-y-auto p-4 space-y-4 min-h-0 bg-white">
            {messages.map((message) => (
              <div
                key={message.timestamp}
                className={`flex items-start gap-2 ${message.role === 'user' ? 'justify-end' : 'justify-start'}`}
              >
                {message.role === 'assistant' && (
                  <Bot className="w-6 h-6 text-primary mt-1" />
                )}
                <div
                  className={`max-w-[85%] rounded-lg p-3 ${message.role === 'user'
                    ? 'bg-primary text-white'
                    : 'bg-gray-100 text-gray-800'}`}
                >
                  {message.role === 'user' ? (
                    message.content
                  ) : (
                    <div>
                      <div className="prose max-w-none [&_p]:mt-0 [&_p]:mb-2 last:[&_p]:mb-0 prose-a:text-primary prose-code:text-gray-800 prose-pre:bg-gray-100 prose-pre:text-gray-800 prose-li:text-gray-800 prose-p:text-gray-800 [&_ul]:text-gray-800 [&_li]:marker:text-gray-800 [&_ol]:text-gray-800 prose-strong:text-gray-900 [&_strong]:font-bold">
                        <ReactMarkdown
                          remarkPlugins={[remarkGfm]}
                          components={{
                            a: ({ children, ...props }: any) => (
                              <a {...props} target="_blank" rel="noopener noreferrer" className="text-primary hover:text-primary/80 bg-primary/10 hover:bg-primary/20 px-1 rounded transition-colors">
                                {children}
                              </a>
                            ),
                            code: ({ inline, className, children, ...props }: any) => {
                              const code = String(children).replace(/\n$/, '');

                              if (inline) {
                                return (
                                  <code className="bg-gray-100 text-gray-800 px-1.5 py-0.5 rounded font-mono text-sm" {...props}>
                                    {children}
                                  </code>
                                );
                              }

                              const isCopied = copiedCode === code;

                              return (
                                <div className="relative group">
                                  <button
                                    onClick={() => {
                                      navigator.clipboard.writeText(code);
                                      setCopiedCode(code);
                                    }}
                                    className="absolute right-2 top-2 p-2 rounded-lg bg-gray-200 text-gray-600 hover:text-gray-800 opacity-0 group-hover:opacity-100 transition-opacity"
                                    aria-label="Copy code"
                                  >
                                    {isCopied ? (
                                      <Check className="w-4 h-4 text-green-600" />
                                    ) : (
                                      <Copy className="w-4 h-4" />
                                    )}
                                  </button>
                                  <pre className="bg-gray-50 ring-1 ring-gray-200 text-gray-800 p-3 rounded-lg font-mono text-sm overflow-x-auto my-2">
                                    <code className={className} {...props}>
                                      {children}
                                    </code>
                                  </pre>
                                </div>
                              );
                            }
                          }}
                        >
                          {message.content}
                        </ReactMarkdown>
                      </div>
                      {message.links && message.links.length > 0 && (
                        <div className="mt-2 pt-2 border-t border-gray-200">
                          <div className="text-xs font-medium text-gray-500 mb-1.5">Related Links:</div>
                          <div className="flex flex-wrap gap-2">
                            {message.links.map((link, index) => (
                              <a
                                key={index}
                                href={link.url}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-sm text-primary hover:text-primary/80 font-semibold flex items-center gap-1.5 bg-primary/10 hover:bg-primary/20 px-3 py-1.5 rounded-md transition-colors"
                              >
                                {link.text}
                              </a>
                            ))}
                          </div>
                        </div>
                      )}
                    </div>
                  )}
                </div>
                {message.role === 'user' && (
                  <User className="w-6 h-6 text-white bg-primary rounded-full p-1 mt-1" />
                )}
              </div>
            ))}
            {isLoading && (
              <div className="flex justify-start items-center gap-2">
                <Bot className="w-6 h-6 text-primary mt-1" />
                <div className="bg-gray-100 text-gray-800 rounded-lg p-3 animate-pulse">
                  Thinking...
                </div>
              </div>
            )}
            <div ref={messagesEndRef} />
          </div>
          <form onSubmit={handleSubmit} className="p-4 border-t border-gray-200 bg-white">
            <div className="flex space-x-2">
              <input
                type="text"
                value={input}
                onChange={(e) => setInput(e.target.value)}
                placeholder="Type your message..."
                className="flex-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-gray-800 bg-white placeholder-gray-400"
                disabled={isLoading}
              />
              <button
                type="submit"
                disabled={isLoading || !input.trim()}
                className="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Send
              </button>
            </div>
          </form>
        </div>
      )}
    </div>
  );
}
