// src/main/webapp/app/components/Footer.tsx
import React from 'react';
import { ScreenSettings } from '../models/screenSettings';

interface FooterProps {
    settings: ScreenSettings | null;
}

const Footer: React.FC<FooterProps> = ({ settings }) => {
    return (
        settings?.showFooter && (
            <footer className="bg-white w-full flex justify-evenly items-center" style={{ height: '15vh', position: 'absolute', bottom: 0 }} id="screenFooter">
                {settings?.footerImages.map((image) => (
                    <div key={image} className="flex px-8">
                        <img src={image} alt="" style={{ maxHeight: '13vh' }} />
                    </div>
                ))}
            </footer>
        )
    );
};

export default Footer;
