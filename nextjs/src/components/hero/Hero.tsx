import Image from 'next/image';
import Link from 'next/link';

interface HeroProps {
  title: string;
  description: string;
  backgroundImage?: string;
  ctaText?: string;
  ctaLink?: string;
}

export function Hero({
  title,
  description,
  backgroundImage,
  ctaText,
  ctaLink,
}: HeroProps) {
  return (
    <section className="relative min-h-[80vh] flex items-center justify-center">
      {backgroundImage && (
        <div className="absolute inset-0 z-0">
          <Image
            src={backgroundImage}
            alt={title}
            fill
            className="object-cover"
            priority
          />
          <div className="absolute inset-0 bg-black/50" />
        </div>
      )}

      <div className="relative z-10 max-w-4xl mx-auto px-4 py-20 text-center">
        <h1 className={`text-5xl font-bold mb-6 ${backgroundImage ? 'text-white' : 'text-gray-900'}`}>
          {title}
        </h1>

        <p className={`text-xl mb-8 ${backgroundImage ? 'text-gray-200' : 'text-gray-600'}`}>
          {description}
        </p>

        {ctaText && ctaLink && (
          <Link
            href={ctaLink}
            className="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
          >
            {ctaText}
          </Link>
        )}
      </div>
    </section>
  );
}
