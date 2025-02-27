'use client';

import { usePathname } from 'next/navigation';
import { useEffect, useState } from 'react';
import AdminBar from './AdminBar';

interface AdminBarWrapperProps {
  isPreviewMode: boolean;
}

export default function AdminBarWrapper({ isPreviewMode }: AdminBarWrapperProps) {
  const pathname = usePathname();
  const [postId, setPostId] = useState<string | undefined>(undefined);

  // Extract post ID from preview path if available
  useEffect(() => {
    // First check for preview path
    const previewPathMatch = pathname.match(/\/preview\/(\d+)/);
    if (previewPathMatch && previewPathMatch[1]) {
      setPostId(previewPathMatch[1]);
      return;
    }

    // Then check for post page with data attribute
    if (pathname.startsWith('/post/')) {
      // Look for article with data-post-id attribute
      const articleElement = document.querySelector('article[data-post-id]');
      if (articleElement) {
        const id = articleElement.getAttribute('data-post-id');
        if (id) {
          setPostId(id);
          return;
        }
      }
    }

    // Reset if no post ID found
    setPostId(undefined);
  }, [pathname]);

  // Only show AdminBar in preview mode
  if (!isPreviewMode) {
    return null;
  }

  return <AdminBar postId={postId} isPreviewMode={true} />;
}
