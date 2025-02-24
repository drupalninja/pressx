import { ResultOf } from '@/graphql/client';
import SiteFooter, { SiteFooterProps } from './site-footer/SiteFooter';
import { graphQLClient } from "@/lib/graphql";
import getConfig from 'next/config';

const { publicRuntimeConfig } = getConfig();

type FooterMenuData = ResultOf<typeof FooterMenuQuery>;

async function getFooterMenu() {
  try {
    const data = await graphQLClient.request<FooterMenuData>(FooterMenuQuery);
    return data.menu;
  } catch (error) {
    console.error('Error fetching footer menu:', error);
    return null;
  }
}

export default async function Footer() {
  const footerMenu = await getFooterMenu();
  const menus = footerMenu?.items;

  const links: SiteFooterProps['links'] = menus?.map(item => ({
    title: item.title,
    url: item.url,
  })) || [];

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

const FooterMenuQuery = `
  query FooterMenu {
    menu(id: "footer", idType: NAME) {
      id
      name
      items {
        id
        title
        url
        children {
          nodes {
            id
            title
            url
          }
        }
      }
    }
  }
`;