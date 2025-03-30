'use client';

import { useCallback, useEffect, useState } from 'react';
import { Score } from '../models/score';
import { Team } from '../models/team';
import { dachScreenSettings, ScreenSettings } from '../models/screenSettings';
import ScreenContainer from '../components/ScreenContainer';

type TeamResponse = { [id: number]: Team };
type Round = 'VR' | 'AF' | 'VF' | 'HF';
type RoundResponse = { [round in Round]: TeamResponse };

const expectedScores: { [round in Round]: number } = {
    VR: 3,
    AF: 1,
    VF: 1,
    HF: 1,
};

const roundNames: { [round in Round]: string } = {
    VR: 'Vorrunden',
    AF: 'Achtelfinale',
    VF: 'Viertelfinale',
    HF: 'Halbfinale',
};

type ScoresResponse = {
    id: string;
    name: string;
    rounds: RoundResponse;
};

export default function ScoreScreenPage() {
    /*const searchParams = useSearchParams();

    let round = searchParams.get('round')?.toUpperCase() ?? 'VR';
    if (!['VR', 'AF', 'VF'].includes(round)) {
        round = 'VR';
    }*/

    const [competition, setCompetition] = useState<ScoresResponse | null>(null);
    const [error] = useState<string | null>(null);

    const [currentIndex, setCurrentIndex] = useState(0);
    const [isPaused, setIsPaused] = useState(false);

    const [teamsPerPage, setTeamsPerPage] = useState(8);
    const [settings, setSettings] = useState<ScreenSettings | null>(null);

    const TOURNAMENT_ID = 1;

    useEffect(() => {
        // Refresh DATA every 5 minutes
        const interval = setInterval(loadDACHData, 5 * 60 * 1000);
        setSettings(dachScreenSettings);
        setTeamsPerPage(dachScreenSettings.teamsPerPage!);
        loadDACHData();
        return () => clearInterval(interval);
    }, []);

    const loadDACHData = useCallback(() => {
        fetch(`https://kiosk.hands-on-technology.org/api/events/${TOURNAMENT_ID}/data/rg-scores`)
            .then((response) => response.json())
            .then((data: ScoresResponse) => {
                setCompetition(data);
            })
            .catch((error) => console.error(error.message));
    }, []);

    const advancePage = useCallback(() => {
        setCurrentIndex((prevIndex) => {
            if (!teams || !competition) return 0;
            if (prevIndex + teamsPerPage > teams.length) return 0;
            return (prevIndex + teamsPerPage) % teams.length;
        });
    }, [competition, teamsPerPage]);

    const previousPage = useCallback(() => {
        setCurrentIndex((prevIndex) => {
            if (prevIndex === 0 && competition && teams) {
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

    function createTeams(category: TeamResponse | undefined): Team[] | undefined {
        if (!category || !round) {
            return undefined;
        }
        const teams = [];
        for (const id in category) {
            const team = category[id];
            team.id = +id;

            const scores = sortScores(team);

            const maxScore = scores[0];
            team.scores = team.scores.map((score: Score) => {
                score.highlight = +score.points === maxScore && maxScore > 0 && scores.length > 1;
                return score;
            });

            while (team.scores.length < expectedScores[round]) {
                team.scores.push({ points: 0, highlight: false });
            }
            teams.push(team);
        }
        teams.sort((a: Team, b: Team) => {
            const aScores = sortScores(a);
            const bScores = sortScores(b);

            for (let i = 0; i < aScores.length && i < bScores.length; i++) {
                if (aScores[i] !== bScores[i]) {
                    return bScores[i] - aScores[i];
                }
            }
            return 0;
        });
        return assignRanks(teams);
    }

    function getRoundToShow(rounds: RoundResponse | undefined): TeamResponse | undefined {
        if (!rounds) {
            return undefined;
        }

        if (rounds.HF) {
            round = 'HF';
            return rounds.HF;
        }
        if (rounds.VF) {
            round = 'VF';
            return rounds.VF;
        }
        if (rounds.AF) {
            round = 'AF';
            return rounds.AF;
        }
        if (rounds.VR) {
            round = 'VR';
            return rounds.VR;
        }
        return undefined;
    }

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

    let round: Round | undefined;
    const teams = createTeams(getRoundToShow(competition?.rounds));

    return (
        <ScreenContainer settings={settings}>
            <h1 className="text-gray text-4xl font-bold px-4 py-12 rounded-lg text-center">
                ERGEBNISSE {round && roundNames[round].toUpperCase()}: {competition?.name?.toUpperCase()}
            </h1>

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
