import { getImage } from '@/components/helpers/Utilities';
import { Post as PostType } from '@/types/wordpress';

export default function Post({ post }: { post: PostType }) {
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
