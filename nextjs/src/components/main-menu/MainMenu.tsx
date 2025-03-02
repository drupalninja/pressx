"use client"

import React, { useState, useMemo } from 'react'
import Link from 'next/link'
import Image from 'next/image'
import { usePathname } from 'next/navigation'
import { Button } from "@/components/ui/button"
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet"
import { MainMenuItem, MainMenuProps } from './Types'
import { cn } from "@/lib/utils"

// Helper function to normalize URLs
const normalizeUrl = (url: string): string => {
  // Remove trailing slash except for root path
  if (url === '/') return '';
  return url.endsWith('/') ? url.slice(0, -1) : url;
};

// Helper function to check if a URL is in the active trail
const isInActiveTrail = (itemUrl: string, currentPath: string): boolean => {
  const normalizedItemUrl = normalizeUrl(itemUrl);
  const normalizedPath = normalizeUrl(currentPath);

  // Special case for root path
  if (normalizedItemUrl === '') {
    return normalizedPath === '' || normalizedPath === '/welcome';
  }

  return (
    // Exact match
    normalizedPath === normalizedItemUrl ||
    // Child page
    normalizedPath.startsWith(normalizedItemUrl + '/') ||
    // Special case for trailing segments
    normalizedPath.endsWith(normalizedItemUrl)
  );
};

const MainMenu: React.FC<MainMenuProps> = ({
  modifier,
  linkModifier,
  siteLogo,
  siteLogoWidth,
  siteLogoHeight,
  siteName,
  showLogo,
  showSiteName,
  menuItems,
  ctaLinkCount,
}) => {
  const pathname = usePathname() || '';
  ctaLinkCount = Math.min(ctaLinkCount, menuItems.length);

  // Process menu items with memoization to avoid unnecessary recalculations
  const processedMenuItems = useMemo(() => {
    return menuItems.map((item, index) => {
      const isCTA = index >= menuItems.length - ctaLinkCount;
      const inActiveTrail = isInActiveTrail(item.url, pathname);

      const below = item.below?.map((subItem) => ({
        ...subItem,
        inActiveTrail: isInActiveTrail(subItem.url, pathname),
      }));

      return { ...item, isCTA, inActiveTrail, below };
    });
  }, [menuItems, pathname, ctaLinkCount]);

  const navItems = processedMenuItems.filter(item => !item.isCTA);
  const ctaItems = processedMenuItems.filter(item => item.isCTA);

  return (
    <nav className={`${modifier}`}>
      <div className="mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          <Link
            href="/"
            className={`flex items-center ${!showLogo ? "text-2xl font-bold" : ""}`}
            aria-current={pathname === '/' || pathname === '' ? 'page' : undefined}
          >
            {showLogo && (
              <Image
                src={siteLogo ?? ''}
                alt="Site Logo"
                width={siteLogoWidth ?? 200}
                height={siteLogoHeight ?? 100}
                className="mr-2 transition-all duration-300 ease-in-out transform"
                unoptimized
              />
            )}
            {showSiteName && siteName && <span className="text-2xl">{siteName.split('\n').map((line, index) => (
              <React.Fragment key={index}>
                {line}
                <br />
              </React.Fragment>
            ))}</span>}
          </Link>

          {/* Desktop menu */}
          <div className="hidden lg:flex lg:items-center lg:space-x-6">
            <DesktopMenuItems items={navItems} linkModifier={linkModifier} />
            <div className="flex items-center space-x-6">
              {ctaItems.map((item, index) => (
                <Button
                  key={index}
                  asChild
                  variant={index === ctaItems.length - 1 ? "default" : "outline"}
                  className="text-lg"
                >
                  <Link
                    href={item.url}
                    aria-current={item.inActiveTrail ? 'page' : undefined}
                  >
                    {item.title}
                  </Link>
                </Button>
              ))}
            </div>
          </div>

          {/* Mobile menu */}
          <Sheet>
            <SheetTrigger asChild>
              <Button variant="outline" className="mobile-menu lg:hidden">Menu</Button>
            </SheetTrigger>
            <SheetContent>
              <SheetHeader>
                <SheetTitle>Menu</SheetTitle>
              </SheetHeader>
              <nav className="mt-6">
                <MobileMenuItems items={navItems} linkModifier={linkModifier} />
                <div className="mt-4">
                  {ctaItems.map((item, index) => (
                    <Button
                      key={index}
                      asChild
                      variant={index === ctaItems.length - 1 ? "default" : "outline"}
                      className="w-full mt-2 text-lg"
                    >
                      <Link
                        href={item.url}
                        aria-current={item.inActiveTrail ? 'page' : undefined}
                      >
                        {item.title}
                      </Link>
                    </Button>
                  ))}
                </div>
              </nav>
            </SheetContent>
          </Sheet>
        </div>
      </div>
    </nav>
  )
}

const MobileMenuItems: React.FC<{
  items: MainMenuItem[]
  linkModifier?: string
}> = ({ items, linkModifier }) => {
  return (
    <ul className="space-y-6">
      {items.map((item, index) => (
        <MobileMenuItem key={index} item={item} linkModifier={linkModifier} />
      ))}
    </ul>
  )
}

const MobileMenuItem: React.FC<{
  item: MainMenuItem
  linkModifier?: string
}> = ({ item, linkModifier }) => {
  const [isExpanded, setIsExpanded] = useState(item.inActiveTrail || false);

  // Update expanded state when active trail changes
  React.useEffect(() => {
    if (item.inActiveTrail) {
      setIsExpanded(true);
    }
  }, [item.inActiveTrail]);

  const toggleExpand = (e: React.MouseEvent) => {
    e.preventDefault();
    if (item.below) {
      setIsExpanded(!isExpanded);
    }
  };

  return (
    <li>
      {item.below ? (
        <div>
          <button
            onClick={toggleExpand}
            className={cn(
              "text-lg font-semibold w-full text-left flex justify-between items-center",
              linkModifier,
              item.inActiveTrail ? 'font-bold text-black' : ''
            )}
            aria-expanded={isExpanded}
          >
            {item.title}
            <svg
              className={`w-4 h-4 transition-transform ${isExpanded ? 'rotate-180' : ''}`}
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
              aria-hidden="true"
            >
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          {isExpanded && (
            <ul className="ml-4 mt-3 space-y-3">
              {item.below.map((subItem, subIndex) => (
                <li key={subIndex}>
                  <Link
                    href={subItem.url}
                    className={cn(
                      "block text-base",
                      linkModifier,
                      subItem.inActiveTrail ? 'font-bold text-black' : ''
                    )}
                    aria-current={subItem.inActiveTrail ? 'page' : undefined}
                  >
                    {subItem.title}
                  </Link>
                </li>
              ))}
            </ul>
          )}
        </div>
      ) : (
        <Link
          href={item.url}
          className={cn(
            "block text-lg",
            linkModifier,
            item.inActiveTrail ? 'font-bold text-black' : ''
          )}
          aria-current={item.inActiveTrail ? 'page' : undefined}
        >
          {item.title}
        </Link>
      )}
    </li>
  )
}

const DesktopMenuItems: React.FC<{
  items: MainMenuItem[]
  linkModifier?: string
}> = ({ items, linkModifier }) => {
  const [activeIndex, setActiveIndex] = useState<number | null>(null);
  const menuRef = React.useRef<HTMLDivElement>(null);

  // Set initial active index based on which item is in the active trail
  React.useEffect(() => {
    const activeItemIndex = items.findIndex(item => item.inActiveTrail);
    if (activeItemIndex !== -1) {
      setActiveIndex(activeItemIndex);
    }
  }, [items]);

  const handleMenuClick = (index: number, e: React.MouseEvent) => {
    if (items[index].below && items[index].below.length > 0) {
      e.preventDefault();
      const newActiveIndex = activeIndex === index ? null : index;
      setActiveIndex(newActiveIndex);
    }
  };

  // Handle click outside to close dropdown
  React.useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
        // Don't reset active index if an item is in the active trail
        if (!items.some(item => item.inActiveTrail)) {
          setActiveIndex(null);
        }
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, [menuRef, items]);

  return (
    <div className="flex space-x-8" ref={menuRef}>
      {items.map((item, index) => (
        <div
          key={index}
          className={cn(
            "relative group",
            item.inActiveTrail || activeIndex === index ? "active" : ""
          )}
        >
          {item.below && item.below.length > 0 ? (
            <>
              <button
                onClick={(e) => handleMenuClick(index, e)}
                className={cn(
                  "flex items-center text-lg text-foreground hover:text-primary",
                  linkModifier,
                  item.inActiveTrail ? 'font-bold text-black' : ''
                )}
                aria-expanded={activeIndex === index || item.inActiveTrail}
                aria-haspopup="true"
              >
                {item.title}
                <svg
                  className="w-4 h-4 ml-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                  aria-hidden="true"
                >
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div
                className={cn(
                  "absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-background ring-1 ring-black ring-opacity-5 transition-all duration-300 ease-in-out",
                  (item.inActiveTrail || activeIndex === index)
                    ? "opacity-100 visible"
                    : "opacity-0 invisible group-hover:opacity-100 group-hover:visible"
                )}
                role="menu"
                aria-orientation="vertical"
                aria-labelledby={`menu-button-${index}`}
              >
                <div className="py-2">
                  {item.below.map((subItem, subIndex) => (
                    <Link
                      key={subIndex}
                      href={subItem.url}
                      className={cn(
                        "block px-4 py-3 text-base text-foreground hover:bg-muted",
                        linkModifier,
                        subItem.inActiveTrail ? 'font-bold text-black' : ''
                      )}
                      role="menuitem"
                      aria-current={subItem.inActiveTrail ? 'page' : undefined}
                    >
                      {subItem.title}
                    </Link>
                  ))}
                </div>
              </div>
            </>
          ) : (
            <Link
              href={item.url}
              className={cn(
                "text-lg text-foreground hover:text-primary",
                linkModifier,
                item.inActiveTrail ? 'font-bold text-black' : ''
              )}
              aria-current={item.inActiveTrail ? 'page' : undefined}
            >
              {item.title}
            </Link>
          )}
        </div>
      ))}
    </div>
  )
}

export default MainMenu
