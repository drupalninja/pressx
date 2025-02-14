import { graphQLClient } from '@/lib/graphql';
import type { Post } from '@/types/wordpress';

const query = `
  query Post($id: ID!) {
    post(id: $id, idType: DATABASE_ID) {
      id
      title
      date
      content
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
  const data = await graphQLClient.request<{ post: Post }>(query, { id });
  const post = data.post;

  return (
    <main className="flex min-h-screen flex-col items-center justify-between p-24">
      <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
        <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
          <p className="font-bold">Preview Mode</p>
          <p>This is a preview of your post. It may not reflect the final published version.</p>
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
