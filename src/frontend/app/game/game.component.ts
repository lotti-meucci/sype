import { Component, ElementRef, ViewChild } from '@angular/core';
import { Difficulty } from 'app/interfaces/difficulty';
import { SypeApiService } from 'app/services/sype-api.service';

const color = [
  "success",
  "warning",
  "danger",
]

const quotes = [
  '"If you\'re in control, you\'re not going fast enough." ~ Parnelli Jones',
  '"I feel the need... the need for speed." ~ Tom Cruise',
  '"Faster, Faster, until the thrill of speed overcomes the fear of death." ~ Hunter S. Thompson',
  '"There are no speed limits on the road to success." ~ David W. Johnson',
  '"I feel very comfortable going at full speed." ~ Steve Nash',
  '"Speed, agility and responsiveness are the keys to future success." ~ Anita Roddick',
  '"Speed has become an important element of strategy." ~ Regis McKenna',
  '"A new beauty has been added to the splendor of the world - the beauty of speed." ~ Filippo Tommaso Marinetti',
  '"We are all burning in time, but each is consumed at his own speed." ~ Jack Gilbert',
  '"I have two speeds. Fast and faster. I don\'t just run. I take it." ~ Arjen Robben'
]

@Component({
  selector: 'app-game',
  templateUrl: './game.component.html',
  styleUrls: ['./game.component.css']
})
export class GameComponent {
  @ViewChild('textarea') textarea?: ElementRef<HTMLTextAreaElement>;
  private _playing!: boolean;
  randomQuote = '';
  randomText = '';
  hideMenu = false;
  hideGame = true;
  hideStartAlert = false;
  selectedDifficulty!: Difficulty;
  difficulties: Difficulty[] = [];

  set playing(v: boolean) {
    sessionStorage.setItem('playing', String(v));
    this._playing = v;
  }

  get playing(): boolean {
    return this._playing;
  }

  constructor(private api: SypeApiService) {
    this.playing = false;

    this.api.getDifficulties().subscribe(data => {
      let i = 0;

      for (const difficulty of data) {
        if (i >= color.length)
          i = 0;

        difficulty.color = color[i++];
      }

      this.selectedDifficulty = data[0];
      this.difficulties = data;
    });

    this.randomQuote = quotes.sort(() => Math.random() - .5)[0];
  }

  startGame() {
    this.hideMenu = true;

    setTimeout(() => {
      this.playing = true;
      this.loadGame();
    }, 250);
  }

  loadGame() {
    this.api.postStartGame({ difficulty: this.selectedDifficulty.level }).subscribe(data => {
      this.randomText = data.text;
      this.hideGame = false;
      setTimeout(() => this.hideStartAlert = true, 1000);
    })
  }

  onTextareaPaste() {
    return false;
  }
}
