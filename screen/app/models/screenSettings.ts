export class ScreenSettings {
    id: number;
    showFooter: boolean;
    backgroundImage?: string;
    footerImages: string[];
    teamsPerPage?: number;

    constructor(id: number, showFooter: boolean, footerImages: string[], backgroundImage?: string, teamsPerPage?: number) {
        this.id = id;
        this.showFooter = showFooter;
        this.footerImages = footerImages;
        this.backgroundImage = backgroundImage;
        this.teamsPerPage = teamsPerPage;
    }
}

export const dachScreenSettings = new ScreenSettings(0, false, [], '/images/Hintergrund.png', 8);
