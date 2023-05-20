import { Component, Input } from '@angular/core';
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
}
