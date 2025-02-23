import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { getImage } from '@/components/helpers/Utilities';
import Link from 'next/link';

interface Post {
  title: string;
  content: string;
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

interface PostPageData {
  post: Post | null;
}

const getPostQuery = `
  query GetPost($slug: ID!) {
    post(id: $slug, idType: SLUG) {
      title
      content
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
`;

export default async function PostPage({
  params: { slug },
}: {
  params: { slug: string };
}) {
  const data = await graphQLClient.request<PostPageData>(getPostQuery, { slug });

  if (!data?.post) {
    notFound();
  }

  const { post } = data;
  const featuredImage = post.featuredImage?.node
    ? getImage(
      {
        sourceUrl: post.featuredImage.node.sourceUrl,
        altText: post.featuredImage.node.altText,
      },
      'w-full h-64 object-cover mb-8',
      'i43large'
    )
    : null;

  return (
    <main className="container mx-auto px-4 py-8">
      <Link
        href="/post"
        className="inline-block mb-8 text-primary hover:text-primary/80 transition-colors duration-200"
      >
        ← Back to Posts
      </Link>
      <article className="prose prose-lg dark:prose-invert max-w-4xl mx-auto">
        <h1 className="text-4xl font-bold mb-4">{post.title}</h1>
        <div className="flex items-center gap-4 mb-8 text-muted-foreground">
          <time dateTime={post.date}>
            {new Date(post.date).toLocaleDateString()}
          </time>
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
        </div>
        {featuredImage}
        <div
          className="mt-8"
          dangerouslySetInnerHTML={{ __html: post.content }}
        />
      </article>
    </main>
  );
}
