import {SlideContent} from "./slideContent";

export class RobotGameSlideContent extends SlideContent {

    public backgroundImageUrl: string;
    public teamsPerPage: number;
    public showFooter: boolean;
    public footerImages: string[];
    public highlightColor: string;

    public toJSON(): object {
        return {
            type: "RobotGameSlideContent",
            backgroundImageUrl: this.backgroundImageUrl,
            teamsPerPage: this.teamsPerPage,
            showFooter: this.showFooter,
            footerImages: this.footerImages,
            highlightColor: this.highlightColor
        };
    }
}
