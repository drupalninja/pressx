import React from "react";
import LogoCollection, { Logo } from "../logo-collection/LogoCollection";
import { getImage } from '@/components/helpers/Utilities';

export interface SectionLogoCollectionProps {
  title: string;
  logos: Array<{
    sourceUrl: string;
    width?: number;
    height?: number;
    alt?: string;
  }>;
}

export default function SectionLogoCollection({ title, logos }: SectionLogoCollectionProps) {
  const processedLogos: Logo[] = logos.map((logo, index) => ({
    name: `logo-${index}`,
    media: getImage(
      logo,
      "max-w-[100px] md:max-w-[120px] h-auto",
      ["medium", "medium"]
    ),
  }));

  return <LogoCollection title={title} logos={processedLogos} />;
}
