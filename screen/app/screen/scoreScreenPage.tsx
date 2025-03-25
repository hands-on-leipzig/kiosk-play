'use client';

import { useSearchParams } from 'next/navigation';
import { useCallback, useEffect, useState } from 'react';
import { Competition } from '../models/competition';
import { Score } from '../models/score';
import { Team } from '../models/team';
import Footer from '../components/Footer';
import { dachScreenSettings, ScreenSettings } from '../models/screenSettings';
import ScreenContainer from '../components/ScreenContainer';

export default function ScoreScreenPage() {
    const searchParams = useSearchParams();
    const rawId = searchParams.get('id') ?? '353';
    const id = parseInt(rawId, 10);

    const [competition, setCompetition] = useState<Competition | null>(null);
    const [error, setError] = useState<string | null>(null);

    const [currentIndex, setCurrentIndex] = useState(0);
    const [isPaused, setIsPaused] = useState(false);

    const [teamsPerPage, setTeamsPerPage] = useState(8);
    const [settings, setSettings] = useState<ScreenSettings | null>(null);

    useEffect(() => {
        /*loadCompetition(id)
            .then((competition) => {
                setCompetition(competition);
                setTeamsPerPage(calculateTeamsPerPage(competition.categories[0]));
            })
            .catch((error) => setError(error.message)); */

        // Load DACH data
        fetch('https://kiosk.hands-on-technology.org/api/events/1620/data/rg-scores')
            .then((response) => response.json())
            .then((data) => {
                setCompetition(new Competition(0, 1, 'DACH-Finale Siegen', [data]));
            })
            .catch((error) => setError(error.message));

        /*loadScreenSettings(id)
            .then((settings) => {
                setSettings(settings);
                if (settings?.teamsPerPage) {
                    setTeamsPerPage(settings.teamsPerPage);
                }
            })
            .catch((error) => console.error('Error loading settings:', error));*/
        setSettings(dachScreenSettings);
        setTeamsPerPage(dachScreenSettings.teamsPerPage!);
    }, [id]);

    const advancePage = useCallback(() => {
        setCurrentIndex((prevIndex) => {
            if (!competition || prevIndex + teamsPerPage > teams.length) return 0;
            return (prevIndex + teamsPerPage) % teams.length;
        });
    }, [competition, teamsPerPage]);

    const previousPage = useCallback(() => {
        setCurrentIndex((prevIndex) => {
            if (prevIndex === 0 && competition) {
                const teamsLastPage = teams.length % teamsPerPage;
                if (teamsLastPage === 0) {
                    return teams.length - teamsPerPage;
                }
                return teams.length - teamsLastPage;
            }
            return Math.max(prevIndex - teamsPerPage, 0);
        });
    }, [teamsPerPage, competition]);

    useEffect(() => {
        const interval = setInterval(() => {
            if (isPaused) return;
            advancePage();
        }, 15000);

        return () => clearInterval(interval);
    }, [competition, teamsPerPage, isPaused, advancePage]);

    useEffect(() => {
        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Enter') {
                console.log('pausing');
                setIsPaused((prev) => !prev);
            } else if (event.key === 'ArrowRight' || event.key === 'ArrowUp') {
                advancePage();
            } else if (event.key === 'ArrowLeft' || event.key === 'ArrowDown') {
                previousPage();
            }
        };

        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [isPaused, competition, teamsPerPage, advancePage, previousPage]);

    function renderScoreCell(score: Score, index: number) {
        const background = score.highlight ? 'blue' : 'none';
        return (
            <td className="px-4 border-t border-r border-l border-white text-center" key={index} style={{ background }}>
                {score.points}
            </td>
        );
    }

    // @ts-expect-error - different data structure when using kiosk API
    const vr = competition?.categories[0]['rounds']['VR'];
    console.log(vr);
    const teams =
        vr &&
        Object.keys(vr)
            ?.map((key) => {
                const team = vr[key];
                team.id = key;

                const maxScore = Math.max(...team.scores.map((score: Score) => +score.points));
                team.scores = team.scores.map((score: Score) => {
                    score.highlight = +score.points === maxScore;
                    return score;
                });

                return team;
            })
            ?.sort((a: Team, b: Team) => {
                const aMax = Math.max(...a.scores.map((score: Score) => +score.points));
                const bMax = Math.max(...b.scores.map((score: Score) => +score.points));

                return bMax - aMax;
            })
            .map((team: Team, index: number) => {
                team.rank = index + 1;
                return team;
            });
    console.log(teams);

    // const teams = competition?.categories[0].teams;

    return (
        <ScreenContainer settings={settings}>
            <h1 className="text-white text-4xl font-bold bg-black/50 px-4 py-12 rounded-lg text-center">ROBOT-GAME SCORE: {competition?.name?.toUpperCase()}</h1>

            <div className="text-white text-5xl bg-black/50 rounded-lg p-20">
                {error && <div className="text-red-500">{error}</div>}
                <table className="w-full border-collapse table-fixed text-left text-white ">
                    <thead>
                        <tr>
                            <th className="px-4 py-2 border-b border-r border-white w-auto">Team</th>
                            <th className="px-4 py-2 border-r border-b border-white text-center w-40">R I</th>
                            <th className="px-4 py-2 border-r border-b border-white text-center w-40">R II</th>
                            <th className="px-4 py-2 border-r border-b border-white text-center w-40">R III</th>
                            <th className="px-4 py-2 border-b border-white text-center w-40">Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        {teams?.slice(currentIndex, Math.min(currentIndex + teamsPerPage, teams?.length)).map((team: Team) => (
                            <tr key={team.id}>
                                <td className="px-4 py-2 border-t border-white">{team.name}</td>
                                {team.scores.map((score, index) => renderScoreCell(score, index))}
                                <td className="px-4 py-2 border-t border-white text-center">{team.rank}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <Footer settings={settings} />
        </ScreenContainer>
    );
}
