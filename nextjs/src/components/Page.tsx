import { getImage } from '@/components/helpers/Utilities';
import { Page as PageType } from '@/types/wordpress';

export default function Page({ page }: { page: PageType }) {
  const featuredImage = page.featuredImage?.node
    ? getImage(
      {
        sourceUrl: page.featuredImage.node.sourceUrl,
        altText: page.featuredImage.node.altText,
      },
      'w-full h-full object-cover',
      'i169large'
    )
    : null;

  return (
    <article className="mb-8" data-page-id={page.databaseId}>
      <div className="mx-auto max-w-7xl p-4 sm:px-6 lg:px-8">
        {featuredImage && (
          <div className="relative aspect-[16/9] mb-6">
            {featuredImage}
          </div>
        )}
        <div className="mx-auto max-w-2xl">
          <h1 className="text-4xl font-bold mb-8">{page.title}</h1>
          <div
            className="prose prose-lg"
            dangerouslySetInnerHTML={{ __html: page.content }}
          />
        </div>
      </div>
    </article>
  );
}
