'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useState, useEffect } from 'react';

interface AdminBarProps {
  postId?: string;
  isPreviewMode: boolean;
}

export default function AdminBar({ postId, isPreviewMode }: AdminBarProps) {
  const [isVisible, setIsVisible] = useState(true);
  const pathname = usePathname();

  // Only show admin bar in preview mode
  if (!isPreviewMode) return null;

  const wpAdminUrl = process.env.NEXT_PUBLIC_WORDPRESS_API_URL?.replace('/graphql', '') || 'https://pressx.ddev.site';

  // Determine if we're on a preview page
  const isPreviewPage = pathname.startsWith('/preview/');

  // Determine if we're on a published post page
  const isPostPage = pathname.startsWith('/post/');

  // Create edit URL if we have a post ID
  const editUrl = postId ? `${wpAdminUrl}/wp-admin/post.php?post=${postId}&action=edit` : `${wpAdminUrl}/wp-admin/`;

  // Toggle admin bar visibility
  const toggleVisibility = () => {
    setIsVisible(!isVisible);
    // Store preference in localStorage
    localStorage.setItem('adminBarVisible', String(!isVisible));
  };

  // Load visibility preference from localStorage on client side
  useEffect(() => {
    const storedVisibility = localStorage.getItem('adminBarVisible');
    if (storedVisibility !== null) {
      setIsVisible(storedVisibility === 'true');
    }
  }, []);

  if (!isVisible) {
    return (
      <button
        onClick={toggleVisibility}
        className="fixed top-2 right-2 bg-black text-white p-2 rounded-full z-50 shadow-lg"
        title="Show Admin Bar"
      >
        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
      </button>
    );
  }

  return (
    <div className="fixed top-0 left-0 right-0 bg-black text-white p-2 z-50 flex justify-between items-center">
      <div className="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        {isPreviewPage ? (
          <span className="font-medium">Preview Mode</span>
        ) : isPostPage ? (
          <span className="font-medium">Edit Mode</span>
        ) : (
          <span className="font-medium">Admin Mode</span>
        )}
        {postId && (
          <span className="ml-2 text-xs bg-gray-700 px-2 py-1 rounded">ID: {postId}</span>
        )}
      </div>
      <div className="flex items-center space-x-4">
        <a
          href={editUrl}
          target="_blank"
          rel="noopener noreferrer"
          className="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center"
        >
          <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          {postId ? 'Edit This Post' : 'WordPress Admin'}
        </a>
        {!pathname.startsWith('/') && (
          <Link
            href="/"
            className="text-gray-300 hover:text-white text-sm flex items-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Home
          </Link>
        )}
        <button
          onClick={toggleVisibility}
          className="text-gray-300 hover:text-white text-sm flex items-center"
          title="Hide Admin Bar"
        >
          <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
          </svg>
          Hide
        </button>
      </div>
    </div>
  );
}
