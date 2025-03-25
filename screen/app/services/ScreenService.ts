import { Competition } from '../models/competition';
import { ScreenSettings } from '../models/screenSettings';
import { Category } from '../models/category';

const BASE_URL = process.env.NEXT_PUBLIC_BASE_URL;

export async function loadCompetition(id: number): Promise<Competition> {
    const response = await fetch(BASE_URL + '/api/parse?id=' + id);
    const competition: Competition = await response.json();

    if (!competition?.categories?.length) {
        throw new Error('No scores found for this competition');
    }
    return competition;
}

export async function loadQFCategory(id: number): Promise<Category> {
    const response = await fetch(BASE_URL + '/api/quarter?id=' + id);
    return await response.json();
}

export async function loadTestround(id: number): Promise<Category> {
    const response = await fetch(BASE_URL + '/api/testround?id=' + id);
    return await response.json();
}

export function calculateTeamsPerPage(category: Category): number {
    const teams = category.teams;
    let pages = 3;
    if (teams.length < 8) {
        pages = 1;
    } else if (teams.length < 16) {
        pages = 2;
    }
    let perPage = Math.ceil(teams.length / pages);
    let teamsLastPage = teams.length % perPage;

    while (teamsLastPage > 0 && teamsLastPage < 4 && teams.length > 8) {
        // To avoid a page with less than 4 teams, we reduce the number of teams per page
        perPage--;
        teamsLastPage = teams.length % perPage;
    }
    return perPage;
}

export async function loadScreenSettings(id: number): Promise<ScreenSettings> {
    const response = await fetch(BASE_URL + '/api/settings?id=' + id);
    return await response.json();
}

export async function fetchBackgroundImage(immageUrl?: string): Promise<string | null> {
    if (!immageUrl) {
        return null;
    }
    try {
        const response = await fetch(BASE_URL + immageUrl);
        const blob = await response.blob();
        return URL.createObjectURL(blob);
    } catch (error) {
        console.error('Error loading background image:', error);
    }
    return null;
}
