import { previewClient } from '@/lib/graphql';
import type { Post } from '@/types/wordpress';
import { cookies } from 'next/headers';
import { Section, SectionResolver, sectionsFragment } from '@/components/sections/SectionResolver';

// Define the Landing type
interface Landing {
  id: string;
  title: string;
  status: string;
  databaseId: number;
  sections: Section[];
}

// Query for regular posts
const postQuery = `
  query Post($id: ID!) {
    post(id: $id, idType: DATABASE_ID) {
      id
      title
      date
      content
      status
      featuredImage {
        node {
          sourceUrl
          altText
        }
      }
    }
  }
`;

// Query for landing pages
const landingQuery = `
  ${sectionsFragment}
  query Landing($id: ID!) {
    landing(id: $id, idType: DATABASE_ID) {
      id
      title
      status
      databaseId
      sections {
        ...Sections
      }
    }
  }
`;

export default async function PreviewPage({
  params: { id },
}: {
  params: { id: string };
}) {
  try {
    console.log(`Fetching preview for content ID: ${id}`);

    // Check if we have a JWT token in cookies
    const cookieStore = cookies();
    const jwtToken = cookieStore.get('wp_jwt_token')?.value;

    if (!jwtToken) {
      console.warn('No JWT token found in cookies. Authentication may fail.');
    } else {
      console.log('JWT token found in cookies. Using for authentication.');
    }

    // First try to fetch as a post
    let postData: { post: Post } | null = null;
    let landingData: { landing: Landing } | null = null;
    let contentType: 'post' | 'landing' | null = null;

    try {
      // Try to fetch as a regular post first
      postData = await previewClient.request<{ post: Post }>(postQuery, { id });
      if (postData?.post) {
        contentType = 'post';
        console.log('Content found as a regular post');
      }
    } catch (postError) {
      console.log('Content not found as a regular post, trying landing page');
    }

    // If not a post, try as a landing page
    if (!postData?.post) {
      try {
        landingData = await previewClient.request<{ landing: Landing }>(landingQuery, { id });
        if (landingData?.landing) {
          contentType = 'landing';
          console.log('Content found as a landing page');
        }
      } catch (landingError) {
        console.log('Content not found as a landing page either');
      }
    }

    // If neither post nor landing page was found
    if (!postData?.post && !landingData?.landing) {
      return (
        <main className="flex min-h-screen flex-col items-center justify-between p-24">
          <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
            <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8" role="alert">
              <p className="font-bold">Error</p>
              <p>Post with ID {id} not found. Make sure you're using the correct ID and that preview mode is enabled.</p>
              <p className="mt-2">Check that the WordPress GraphQL API is properly configured to allow access to private posts.</p>
              <p className="mt-2">JWT Authentication Status: {jwtToken ? 'Token Present' : 'No Token Found'}</p>
            </div>
          </div>
        </main>
      );
    }

    // Render post preview
    if (contentType === 'post' && postData?.post) {
      const post = postData.post;

      return (
        <main className="flex min-h-screen flex-col items-center justify-between p-24" data-post-id={id} data-post-type="post">
          <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
            <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
              <p className="font-bold">Preview Mode</p>
              <p>This is a preview of your post. It may not reflect the final published version.</p>
              <p className="mt-2">Post Status: <span className="font-semibold">{post.status}</span></p>
              <p className="mt-2">Post ID: <span className="font-semibold">{id}</span></p>
              <p className="mt-2">Authentication: <span className="font-semibold">{jwtToken ? 'JWT Authenticated' : 'Not Authenticated'}</span></p>
            </div>

            <article className="p-6 bg-white rounded-lg shadow">
              {post.featuredImage && (
                <img
                  src={post.featuredImage.node.sourceUrl}
                  alt={post.featuredImage.node.altText}
                  className="w-full h-auto mb-4 rounded"
                />
              )}
              <h1 className="text-4xl font-bold mb-2">{post.title}</h1>
              <div className="text-gray-600 mb-4">
                {new Date(post.date).toLocaleDateString()}
              </div>
              <div
                className="prose max-w-none"
                dangerouslySetInnerHTML={{ __html: post.content }}
              />
            </article>
          </div>
        </main>
      );
    }

    // Render landing page preview
    if (contentType === 'landing' && landingData?.landing) {
      const landing = landingData.landing;

      return (
        <main className="min-h-screen" data-post-id={id} data-post-type="landing">
          <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
            <p className="font-bold">Preview Mode</p>
            <p>This is a preview of your landing page. It may not reflect the final published version.</p>
            <p className="mt-2">Page Status: <span className="font-semibold">{landing.status}</span></p>
            <p className="mt-2">Page ID: <span className="font-semibold">{id}</span></p>
            <p className="mt-2">Authentication: <span className="font-semibold">{jwtToken ? 'JWT Authenticated' : 'Not Authenticated'}</span></p>
          </div>

          {landing.sections?.map((section: Section, index: number) => (
            <SectionResolver key={index} section={section} />
          ))}
        </main>
      );
    }

  } catch (error) {
    console.error('Error fetching content:', error);

    // Check if we have a JWT token in cookies
    const cookieStore = cookies();
    const jwtToken = cookieStore.get('wp_jwt_token')?.value;

    return (
      <main className="flex min-h-screen flex-col items-center justify-between p-24">
        <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
          <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8" role="alert">
            <p className="font-bold">Error</p>
            <p>Failed to fetch content with ID {id}. Make sure preview mode is enabled and authentication is configured correctly.</p>
            <p className="mt-2">Error details: {error instanceof Error ? error.message : String(error)}</p>
            <p className="mt-2">JWT Authentication Status: {jwtToken ? 'Token Present' : 'No Token Found'}</p>
            <p className="mt-2">Check the browser console for more details.</p>
          </div>
        </div>
      </main>
    );
  }
}
