import { Component, ElementRef, ViewChild } from '@angular/core';

@Component({
  selector: 'app-game',
  templateUrl: './game.component.html',
  styleUrls: ['./game.component.css']
})
export class GameComponent {
  visible = false;
  over(){
    this.visible = true;
 }

 out(){
  this.visible = false;
 }
}
