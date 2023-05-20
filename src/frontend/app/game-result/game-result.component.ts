import { Component, Input } from '@angular/core';
import { Difficulty } from 'app/types/difficulty';
import { Game } from 'app/types/game';

@Component({
  selector: 'app-game-result',
  templateUrl: './game-result.component.html',
  styleUrls: ['./game-result.component.css']
})
export class GameResultComponent {
  @Input() game?: Game;
  @Input() position?: number;
  @Input() first = false;
  @Input() difficultiesData?: Difficulty[];

  getDifficultyData(level?: number): Difficulty | undefined {
    return this.difficultiesData?.filter(d => d.level == level)[0];
  }
}
