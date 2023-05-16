import { Component, ElementRef, ViewChild } from '@angular/core';
import { Difficulty } from 'app/interfaces/difficulty';
import { SypeApiService } from 'app/services/sype-api.service';

const colors = [
  "btn-success",
  "btn-warning",
  "btn-danger",
]

@Component({
  selector: 'app-game',
  templateUrl: './game.component.html',
  styleUrls: ['./game.component.css']
})
export class GameComponent {
  difficulties: Difficulty[] = [];

  constructor(private api: SypeApiService){
    this.api.getDifficulties().subscribe(data => {
      let iterations = 0;

      for (let i = 0; i < data.length; i++)
      {
        if (i % colors.length == 0)
          iterations++;

        data[i].color = colors[i - colors.length * iterations];
      }

      this.difficulties = data;
    });
  }
}
