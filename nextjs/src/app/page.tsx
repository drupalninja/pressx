import { graphQLClient } from '@/lib/graphql';
import type { Post, PostsResponse } from '@/types/wordpress';

const query = `
  query Posts {
    posts {
      nodes {
        id
        title
        date
        excerpt
      }
    }
  }
`;

export default async function Home() {
  const data = await graphQLClient.request<PostsResponse>(query);
  const posts = data.posts.nodes;

  return (
    <main className="flex min-h-screen flex-col items-center justify-between p-24">
      <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
        <h1 className="text-4xl font-bold mb-8">Latest Posts</h1>
        <div className="grid gap-8">
          {posts.map((post: Post) => (
            <article key={post.id} className="p-6 bg-white rounded-lg shadow">
              <h2 className="text-2xl font-semibold mb-2">{post.title}</h2>
              <div className="text-gray-600 mb-4">
                {new Date(post.date).toLocaleDateString()}
              </div>
              <div
                className="prose"
                dangerouslySetInnerHTML={{ __html: post.excerpt }}
              />
            </article>
          ))}
        </div>
      </div>
    </main>
  );
}
