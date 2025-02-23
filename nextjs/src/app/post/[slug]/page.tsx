import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { getImage } from '@/components/helpers/Utilities';
import Link from 'next/link';

interface Post {
  title: string;
  content: string;
  excerpt: string;
  date: string;
  featuredImage?: {
    node: {
      sourceUrl: string;
      altText?: string;
      mediaDetails?: {
        width: number;
        height: number;
      };
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
      excerpt
      date
      featuredImage {
        node {
          sourceUrl
          altText
          mediaDetails {
            width
            height
          }
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
        width: post.featuredImage.node.mediaDetails?.width,
        height: post.featuredImage.node.mediaDetails?.height,
      },
      'w-full h-full object-cover',
      'i169large'
    )
    : null;

  return (
    <article className="mb-8">
      <div className="mx-auto max-w-7xl p-4 sm:px-6 lg:px-8">
        {featuredImage && (
          <div className="relative aspect-[16/9] mb-6">
            {featuredImage}
          </div>
        )}
        <div className="mx-auto max-w-2xl">
          {post.tags?.nodes && post.tags.nodes.length > 0 && (
            <div className="uppercase mb-2 text-sm tracking-wide">
              {post.tags.nodes.map((tag) => tag.name).join(', ')}
            </div>
          )}
          <h1 className="text-4xl font-bold mb-8">{post.title}</h1>
          {post.excerpt && (
            <div
              className="prose prose-lg lead mb-4"
              dangerouslySetInnerHTML={{ __html: post.excerpt }}
            />
          )}
          <div
            className="prose prose-lg"
            dangerouslySetInnerHTML={{ __html: post.content }}
          />
          <div className="mt-8 pt-8 border-t border-border">
            <Link
              href="/post"
              className="text-primary hover:text-primary/80 transition-colors duration-200"
            >
              ← Back to Posts
            </Link>
          </div>
        </div>
      </div>
    </article>
  );
}
