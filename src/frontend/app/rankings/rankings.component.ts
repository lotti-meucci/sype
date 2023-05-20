import { Component } from '@angular/core';
import { SypeApiService } from 'app/services/sype-api.service';
import { Difficulty } from 'app/types/difficulty';
import { Game } from 'app/types/game';

@Component({
  selector: 'app-rankings',
  templateUrl: './rankings.component.html',
  styleUrls: ['./rankings.component.css']
})
export class RankingsComponent {
  private _selectedDifficulty!: Difficulty;
  difficulties: Difficulty[] = [];
  games: Game[] = [];

  set selectedDifficulty(v: Difficulty) {
    this._selectedDifficulty = v;
    this.api.getRankings(v.level).subscribe(data => this.games = data);
  }

  get selectedDifficulty(): Difficulty {
    return this._selectedDifficulty;
  }

  constructor(private api: SypeApiService) {
    this.api.getDifficulties().subscribe(data => {
      this.selectedDifficulty = data[0];
      this.difficulties = data;
    });
  }
}
