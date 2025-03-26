'use client';

import { useSearchParams } from 'next/navigation';
import { useCallback, useEffect, useState } from 'react';
import { Competition } from '../models/competition';
import { Score } from '../models/score';
import { Team } from '../models/team';
import { dachScreenSettings, ScreenSettings } from '../models/screenSettings';
import ScreenContainer from '../components/ScreenContainer';

export default function ScoreScreenPage() {
    const searchParams = useSearchParams();

    let round = searchParams.get('round')?.toUpperCase() ?? 'VR';
    if (!['VR', 'AF', 'VF'].includes(round)) {
        round = 'VR';
    }

    const [competition, setCompetition] = useState<Competition | null>(null);
    const [error, setError] = useState<string | null>(null);

    const [currentIndex, setCurrentIndex] = useState(0);
    const [isPaused, setIsPaused] = useState(false);

    const [teamsPerPage, setTeamsPerPage] = useState(8);
    const [settings, setSettings] = useState<ScreenSettings | null>(null);

    const TOURNAMENT_ID = 1;

    useEffect(() => {
        // Load DACH data
        fetch(`https://kiosk.hands-on-technology.org/api/events/${TOURNAMENT_ID}/data/rg-scores`)
            .then((response) => response.json())
            .then((data) => {
                setCompetition(new Competition(0, 1, data.name, [data]));
            })
            .catch((error) => setError(error.message));
        setSettings(dachScreenSettings);
        setTeamsPerPage(dachScreenSettings.teamsPerPage!);
    }, []);

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
        const background = score.highlight ? '#F78B1F' : 'none';
        return (
            <td className="px-4 border-t border-r border-l border-white text-center" key={index} style={{ background }}>
                {score.points}
            </td>
        );
    }

    // @ts-expect-error - different data structure when using kiosk API
    const vr = competition?.categories[0]['rounds'][round];
    let teams =
        vr &&
        Object.keys(vr)
            ?.map((key) => {
                const team = vr[key];
                team.id = key;

                const scores = sortScores(team);
                const maxScore = scores[0];
                team.scores = team.scores.map((score: Score) => {
                    score.highlight = +score.points === maxScore && maxScore > 0 && scores.length > 1;
                    return score;
                });

                const expectedScores = round === 'VR' ? 3 : 1;
                while (team.scores.length < expectedScores) {
                    team.scores.push({ points: 0, highlight: false });
                }

                return team;
            })
            ?.sort((a: Team, b: Team) => {
                const aScores = sortScores(a);
                const bScores = sortScores(b);

                for (let i = 0; i < aScores.length && i < bScores.length; i++) {
                    if (aScores[i] !== bScores[i]) {
                        return bScores[i] - aScores[i];
                    }
                }
                return 0;
            });
    teams = assignRanks(teams);

    function assignRanks(teams: Team[] | undefined): Team[] | undefined {
        if (!teams || teams.length === 0) {
            return teams;
        }

        let rank = 1;
        let prevScore = 0;
        const result = [];
        for (let i = 0; i < teams.length; i++) {
            const maxScore = sortScores(teams[i])[0];
            if (maxScore !== prevScore) {
                rank = i + 1;
            }
            teams[i].rank = rank;
            if (maxScore > 0 || prevScore == 0) {
                result.push(teams[i]);
                prevScore = maxScore;
            }
        }
        return result;
    }

    function sortScores(team: Team): number[] {
        return team.scores.map((score: Score) => +score.points).sort((a, b) => b - a);
    }

    // const teams = competition?.categories[0].teams;

    return (
        <ScreenContainer settings={settings} style={{position: 'absolute', top: 0}}>
            <h1 className="text-gray text-4xl font-bold px-4 py-12 rounded-lg text-center">ROBOT-GAME SCORE: {competition?.name?.toUpperCase()}</h1>

            <div className="text-gray text-5xl rounded-lg p-20">
                {error && <div className="text-red-500">{error}</div>}
                <table className="w-full border-collapse table-fixed text-left text-gray ">
                    <thead>
                        <tr>
                            <th className="px-4 py-2 border-b border-r border-white w-auto">Team</th>
                            {round === 'VR' ? (
                                <>
                                    <th className="px-4 py-2 border-r border-b border-white text-center w-40">R I</th>
                                    <th className="px-4 py-2 border-r border-b border-white text-center w-40">R II</th>
                                    <th className="px-4 py-2 border-r border-b border-white text-center w-40">R III</th>
                                </>
                            ) : (
                                <th className="px-4 py-2 border-r border-b border-white text-center w-60">Score</th>
                            )}
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
        </ScreenContainer>
    );
}
