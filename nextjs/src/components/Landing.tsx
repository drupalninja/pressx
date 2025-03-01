import { Section, SectionResolver } from '@/components/sections/SectionResolver';
import { Landing as LandingType } from '@/types/wordpress';

// Override the sections type to use the Section from SectionResolver
interface LandingWithCorrectSections extends Omit<LandingType, 'sections'> {
  sections: Section[];
}

export default function Landing({ landing }: { landing: LandingWithCorrectSections }) {
  return (
    <main className="min-h-screen" data-post-id={landing.databaseId} data-post-type="landing">
      {landing.sections?.map((section, index) => (
        <SectionResolver key={index} section={section} />
      ))}
    </main>
  );
}
