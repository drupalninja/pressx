import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { getImage } from '@/components/helpers/Utilities';
import { Metadata } from 'next';

interface Post {
  title: string;
  content: string;
  excerpt: string;
  date: string;
  databaseId: number;
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
      databaseId
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
      },
      'w-full h-full object-cover',
      'i169large'
    )
    : null;

  return (
    <article className="mb-8" data-post-id={post.databaseId}>
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
        </div>
      </div>
    </article>
  );
}

export async function generateMetadata({
  params: { slug },
}: {
  params: { slug: string };
}): Promise<Metadata> {
  try {
    const data = await graphQLClient.request<PostPageData>(getPostQuery, { slug });

    return {
      title: data.post?.title || slug,
    };
  } catch (error) {
    return {
      title: slug,
    };
  }
}
