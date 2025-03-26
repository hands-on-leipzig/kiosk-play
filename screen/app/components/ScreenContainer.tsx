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
    return (
        <div
            className="w-screen h-screen fldex flex-col items-center justify-start bg-cover bg-center"
            style={{
                background: "white",
                ...style,
            }}
        >
            {children}
        </div>
    );
};

export default ScreenContainer;
