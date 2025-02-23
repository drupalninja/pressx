import { graphQLClient } from '@/lib/graphql';
import Link from 'next/link';
import { getImage } from '@/components/helpers/Utilities';

interface Post {
  databaseId: number;
  title: string;
  slug: string;
  excerpt: string;
  date: string;
  featuredImage?: {
    node: {
      sourceUrl: string;
      altText?: string;
    };
  };
  tags?: {
    nodes: Array<{
      name: string;
      slug: string;
    }>;
  };
}

interface BlogPageData {
  posts: {
    nodes: Post[];
  };
}

const getBlogPostsQuery = `
  query GetBlogPosts {
    posts(first: 100) {
      nodes {
        databaseId
        title
        slug
        excerpt
        date
        featuredImage {
          node {
            sourceUrl
            altText
          }
        }
        tags {
          nodes {
            name
            slug
          }
        }
      }
    }
  }
`;

export default async function PostsPage() {
  const data = await graphQLClient.request<BlogPageData>(getBlogPostsQuery);
  const posts = data.posts.nodes;

  return (
    <main className="container mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold mb-8">Posts</h1>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {posts.map((post) => {
          const featuredImage = post.featuredImage?.node
            ? getImage(
              {
                sourceUrl: post.featuredImage.node.sourceUrl,
                altText: post.featuredImage.node.altText,
              },
              'w-full h-48 object-cover rounded-t-lg',
              'i43medium'
            )
            : null;

          return (
            <article
              key={post.databaseId}
              className="bg-card rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300"
            >
              {featuredImage}
              <div className="p-6">
                <h2 className="text-2xl font-bold mb-2">
                  <Link
                    href={`/post/${post.slug}`}
                    className="hover:text-primary transition-colors duration-200"
                  >
                    {post.title}
                  </Link>
                </h2>
                <div
                  className="text-muted-foreground mb-4"
                  dangerouslySetInnerHTML={{ __html: post.excerpt }}
                />
                {post.tags?.nodes && post.tags.nodes.length > 0 && (
                  <div className="flex flex-wrap gap-2">
                    {post.tags.nodes.map((tag) => (
                      <span
                        key={tag.slug}
                        className="px-3 py-1 bg-muted rounded-full text-sm"
                      >
                        {tag.name}
                      </span>
                    ))}
                  </div>
                )}
                <div className="mt-4 text-sm text-muted-foreground">
                  {new Date(post.date).toLocaleDateString()}
                </div>
              </div>
            </article>
          );
        })}
      </div>
    </main>
  );
}
