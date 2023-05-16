import { Component, ElementRef, ViewChild } from '@angular/core';
import { Difficulty } from 'app/interfaces/difficulty';
import { SypeApiService } from 'app/services/sype-api.service';

const colorClasses = [
  "btn-success",
  "btn-warning",
  "btn-danger",
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
  randomQuote = '';
  selectedDifficultyLevel!: number;
  difficulties: Difficulty[] = [];

  constructor(private api: SypeApiService){
    this.api.getDifficulties().subscribe(data => {
      let i = 0;

      for (const difficulty of data) {
        if (i >= colorClasses.length)
          i = 0;

        difficulty.colorClass = colorClasses[i++];
      }

      this.selectedDifficultyLevel = data[0].level;
      this.difficulties = data;
    });

    this.randomQuote = quotes.sort(() => Math.random() - .5)[0];
  }
}
