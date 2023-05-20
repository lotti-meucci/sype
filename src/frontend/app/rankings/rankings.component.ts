import { Component } from '@angular/core';
import { SypeApiService } from 'app/services/sype-api.service';
import { Difficulty } from 'app/types/difficulty';

@Component({
  selector: 'app-rankings',
  templateUrl: './rankings.component.html',
  styleUrls: ['./rankings.component.css']
})
export class RankingsComponent {
  selectedDifficulty!: Difficulty;
  difficulties: Difficulty[] = [];

  constructor(private api: SypeApiService) {
    this.api.getDifficulties().subscribe(data => {
      this.selectedDifficulty = data[0];
      this.difficulties = data;
    });
  }
}
