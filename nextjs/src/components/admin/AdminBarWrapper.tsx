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
  const [postType, setPostType] = useState<string | undefined>(undefined);

  // Extract post ID from path or DOM
  useEffect(() => {
    // First check for preview path
    const previewPathMatch = pathname.match(/\/preview\/(\d+)/);
    if (previewPathMatch && previewPathMatch[1]) {
      setPostId(previewPathMatch[1]);
      setPostType('preview');
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
          setPostType('post');
          return;
        }
      }
    }

    // Check for landing page
    // Landing pages use the root slug pattern /[slug]
    if (!pathname.startsWith('/post/') && !pathname.startsWith('/preview/') && pathname !== '/') {
      // Look for main element with data-post-id attribute
      const mainElement = document.querySelector('main[data-post-id]');
      if (mainElement) {
        const id = mainElement.getAttribute('data-post-id');
        const type = mainElement.getAttribute('data-post-type') || 'landing';
        if (id) {
          setPostId(id);
          setPostType(type);
          return;
        }
      }
    }

    // Reset if no post ID found
    setPostId(undefined);
    setPostType(undefined);
  }, [pathname]);

  // Only show AdminBar in preview mode
  if (!isPreviewMode) {
    return null;
  }

  return <AdminBar postId={postId} postType={postType} isPreviewMode={true} />;
}
