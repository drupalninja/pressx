import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Separator } from "@/components/ui/separator"

export type SiteFooterProps = {
  links: { title: string; url: string | null }[];
  siteLogo?: string;
  siteLogoWidth?: number;
  siteLogoHeight?: number;
  siteName?: string;
  showLogo?: boolean;
  currentYear?: number;
};

const SiteFooter: React.FC<SiteFooterProps> = ({
  links,
  siteLogo,
  siteLogoWidth,
  siteLogoHeight,
  siteName = '',
  showLogo = true,
  currentYear = new Date().getFullYear(),
}) => {
  return (
    <footer className="container mx-auto px-4 mt-6 lg:mt-25">
      <Separator className="my-4" />
      <div className="flex flex-col items-center space-y-4 pt-3 pb-10 md:flex-row md:justify-between md:space-y-0">
        <div className="text-center md:text-left md:w-1/3">
          <p className="text-muted-foreground">
            © {currentYear} {siteName}
          </p>
        </div>

        <div className="flex justify-center md:w-1/3">
          <Link href="/" className="flex items-center justify-center">
            {showLogo && siteLogo && (
              <Image src={siteLogo} width={siteLogoWidth} height={siteLogoHeight} alt={siteName} unoptimized />
            )}
          </Link>
        </div>

        <nav className="md:w-1/3">
          <ul className="flex flex-wrap justify-center space-x-4 md:justify-end">
            {links.map((link, index) =>
              link?.url && (
                <li key={index}>
                  <Link href={link.url} className="text-muted-foreground hover:text-foreground">
                    {link.title}
                  </Link>
                </li>
              )
            )}
          </ul>
        </nav>
      </div>
    </footer>
  );
};

export default SiteFooter;
