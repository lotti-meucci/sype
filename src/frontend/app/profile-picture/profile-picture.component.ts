import { Component, Input } from '@angular/core';
import { SypeApiService } from 'app/services/sype-api.service';

const TOKEN_MIN = 0;
const TOKEN_MAX = 10000;

@Component({
  selector: 'app-profile-picture',
  templateUrl: './profile-picture.component.html',
  styleUrls: ['./profile-picture.component.css']
})
export class ProfilePictureComponent {
  private readonly parentToken = Math.floor(Math.random() * (TOKEN_MAX - TOKEN_MIN) + TOKEN_MIN)
  url: string = "";
  token!: number;

  constructor(private api: SypeApiService) { }

  @Input()
  set nickname(v: string) {
    this.seed = 0;

    if (v)
      this.url = this.api.toPictureUrl(v);
    else
      this.url = "";
  }

  @Input()
  set seed(v: number) {
    this.token = this.parentToken + v;
  }
}
