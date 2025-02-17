import React, { FC } from 'react';
import Image from 'next/image';

interface LogoProps {
  modifier?: string;
  siteLogo?: string;
}

const Logo: FC<LogoProps> = ({ modifier = '', siteLogo = '/images/logo.svg' }) => {
  return (
    <div className={modifier}>
      <Image 
        src={siteLogo} 
        width={312} 
        height={96} 
        className="logo w-auto h-auto" 
        alt="Site logo"
        priority
        unoptimized
      />
    </div>
  );
};

export default Logo;
