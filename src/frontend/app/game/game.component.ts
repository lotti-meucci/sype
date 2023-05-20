import { Component, ElementRef, ViewChild } from '@angular/core';
import { Difficulty } from 'app/types/difficulty';
import { SypeApiService } from 'app/services/sype-api.service';
import { Router } from '@angular/router';
import { catchError } from 'rxjs';
import { ErrorsNumberResponse } from 'app/types/errors-number-response';

declare const bootstrap: any;

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
  @ViewChild('resultModal') resultModal?: ElementRef<HTMLElement>
  private _playing!: boolean;
  randomQuote = '';
  randomText = '';
  hideMenu = false;
  hideGame = true;
  hideStartAlert = false;
  selectedDifficulty!: Difficulty;
  difficulties: Difficulty[] = [];
  timerInterval?: any;
  gameStartTime: number = 0;
  gameCurrentSeconds: number = 0;
  gameEndSeconds: number = 0;
  gameErrorsNumber: number = 0;

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
      this.selectedDifficulty = data[0];
      this.difficulties = data;
    });

    this.randomQuote = quotes.sort(() => Math.random() - .5)[0];
  }

  startGame() {
    this.hideMenu = true;

    setTimeout(() => {
      this.playing = true;
      this.loadGameData();
    }, 250);
  }

  loadGameData() {
    this.api.postStartGame({ difficulty: this.selectedDifficulty.level }).subscribe(data => {
      this.randomText = data.text;
      this.hideGame = false;
      this.gameDataLoaded();
      setTimeout(() => this.hideStartAlert = true, 1250);
    })
  }

  gameDataLoaded() {
    if (this.timerInterval)
      clearInterval(this.timerInterval);

    this.gameStartTime = Date.now();
    this.gameCurrentSeconds = 0;
    this.timerInterval = setInterval(() => this.gameCurrentSeconds = this.getResult());
  }

  endGame(text: string) {
    this.gameEndSeconds = this.getResult();

    this.api.postEndGame({ result: this.gameEndSeconds, text: text }).pipe(
      catchError(() => {
        this.cancelGame();
        return '';
      })
    ).subscribe(data => {
      const res = data as ErrorsNumberResponse;
      this.gameErrorsNumber = res.errorsNumber;
      const modal = new bootstrap.Modal(this.resultModal!.nativeElement);
      modal.show();
    });
  }

  closeGameMode() {
    this.playing = false;
    setTimeout(() => this.hideMenu = false, 100);
  }

  onTextAreaPaste() {
    return false;
  }

  textAreaInput() {
    const text = this.textarea!.nativeElement.value;

    if (text.match(/[\n\r]/)) {
      this.hideGame = true;
      clearInterval(this.timerInterval!);
      this.timerInterval = null;
      this.endGame(text);
    }
  }

  cancelGame() {
    if (this.timerInterval)
    {
      clearInterval(this.timerInterval);
      this.timerInterval = null;
    }

    this.playing = false;
    this.hideGame = true;
    this.hideMenu = false;
  }

  getResult(): number {
    return Math.trunc((Date.now() - this.gameStartTime) / 100) / 10;
  }
}
