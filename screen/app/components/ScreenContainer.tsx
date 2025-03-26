'use client';

import React, { CSSProperties } from 'react';
import { ScreenSettings } from '../models/screenSettings';

interface ScreenContainerProps {
    settings: ScreenSettings | null;
    children: React.ReactNode;
    style?: CSSProperties;
}

const ScreenContainer: React.FC<ScreenContainerProps> = ({ children, style }) => {
    return (
        <div
            className="w-screen h-screen fldex flex-col items-center justify-start bg-cover bg-center"
            style={{
                background: 'black',
                ...style,
            }}
        >
            {children}
        </div>
    );
};

export default ScreenContainer;
