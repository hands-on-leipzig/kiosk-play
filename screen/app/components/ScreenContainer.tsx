'use client';

import React, { CSSProperties, useEffect, useState } from 'react';
import { ScreenSettings } from '../models/screenSettings';
import { fetchBackgroundImage } from '../services/ScreenService';

interface ScreenContainerProps {
    settings: ScreenSettings | null;
    children: React.ReactNode;
    style?: CSSProperties;
}

const ScreenContainer: React.FC<ScreenContainerProps> = ({ settings, children, style }) => {
    const [backgroundImage, setBackgroundImage] = useState<string | null>(null);

    useEffect(() => {
        if (settings?.backgroundImage) {
            fetchBackgroundImage(settings.backgroundImage).then(setBackgroundImage);
        }
    }, [settings]);

    return (
        <div
            className="w-screen h-screen fldex flex-col items-center justify-start bg-cover bg-center"
            style={{
                backgroundImage: backgroundImage ? `url(${backgroundImage})` : 'none',
                ...style,
            }}
        >
            {children}
        </div>
    );
};

export default ScreenContainer;
