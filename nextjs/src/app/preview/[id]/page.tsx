import { previewClient } from '@/lib/graphql';
import type { Post } from '@/types/wordpress';
import { cookies } from 'next/headers';

const query = `
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

export default async function PreviewPage({
  params: { id },
}: {
  params: { id: string };
}) {
  try {
    console.log(`Fetching preview for post ID: ${id}`);

    // Check if we have a JWT token in cookies
    const cookieStore = cookies();
    const jwtToken = cookieStore.get('wp_jwt_token')?.value;

    if (!jwtToken) {
      console.warn('No JWT token found in cookies. Authentication may fail.');
    } else {
      console.log('JWT token found in cookies. Using for authentication.');
    }

    // Use the previewClient which includes JWT authentication from cookies
    const data = await previewClient.request<{ post: Post }>(query, { id });

    console.log(`Preview data received:`, data);

    const post = data.post;

    if (!post) {
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

    return (
      <main className="flex min-h-screen flex-col items-center justify-between p-24">
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
  } catch (error) {
    console.error('Error fetching post:', error);

    // Check if we have a JWT token in cookies
    const cookieStore = cookies();
    const jwtToken = cookieStore.get('wp_jwt_token')?.value;

    return (
      <main className="flex min-h-screen flex-col items-center justify-between p-24">
        <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
          <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8" role="alert">
            <p className="font-bold">Error</p>
            <p>Failed to fetch post with ID {id}. Make sure preview mode is enabled and authentication is configured correctly.</p>
            <p className="mt-2">Error details: {error instanceof Error ? error.message : String(error)}</p>
            <p className="mt-2">JWT Authentication Status: {jwtToken ? 'Token Present' : 'No Token Found'}</p>
            <p className="mt-2">Check the browser console for more details.</p>
          </div>
        </div>
      </main>
    );
  }
}
