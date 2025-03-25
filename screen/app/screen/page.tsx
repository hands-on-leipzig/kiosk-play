'use client';

import { Suspense } from 'react';
import ScoreScreenPage from './scoreScreenPage';

export default function ScreenPageWrapper() {
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <ScoreScreenPage />
        </Suspense>
    );
}
