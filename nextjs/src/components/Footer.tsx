import SiteFooter, { SiteFooterProps } from './site-footer/SiteFooter';
import { graphQLClient } from "@/lib/graphql";
import getConfig from 'next/config';

const { publicRuntimeConfig } = getConfig();

const FooterMenuQuery = `
  query FooterMenu {
    menuItems(where: { location: FOOTER }) {
      nodes {
        id
        label
        url
      }
    }
  }
`;

async function getFooterMenu() {
  try {
    console.log('Fetching footer menu data from:', process.env.NEXT_PUBLIC_WORDPRESS_API_URL);
    const data = await graphQLClient.request(FooterMenuQuery);
    console.log('Raw GraphQL response:', JSON.stringify(data, null, 2));
    return data.menuItems;
  } catch (error) {
    console.error('Error fetching footer menu. Full error:', error);
    if (error instanceof Error) {
      console.error('Error message:', error.message);
    }
    return null;
  }
}

export default async function Footer() {
  const menuItems = await getFooterMenu();
  console.log('Footer menu items:', menuItems?.nodes);

  const links: SiteFooterProps['links'] = menuItems?.nodes?.map(item => ({
    title: item.label,
    url: item.url,
  })) || [];
  console.log('Transformed footer links:', links);

  return (
    <SiteFooter
      links={links}
      siteLogo={publicRuntimeConfig?.LOGO_URL || '/images/logo.svg'}
      siteLogoWidth={parseInt(publicRuntimeConfig?.LOGO_WIDTH || '160')}
      siteLogoHeight={parseInt(publicRuntimeConfig?.LOGO_HEIGHT || '42')}
      siteName={publicRuntimeConfig?.SITE_NAME || 'PressX'}
      showLogo={publicRuntimeConfig?.SHOW_LOGO !== '0'}
    />
  );
}
