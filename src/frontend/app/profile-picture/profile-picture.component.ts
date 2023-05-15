import { Component, Input } from '@angular/core';
import { SypeApiService } from 'app/services/sype-api.service';

@Component({
  selector: 'app-profile-picture',
  templateUrl: './profile-picture.component.html',
  styleUrls: ['./profile-picture.component.css']
})
export class ProfilePictureComponent {
  url: string = "";

  constructor(private api: SypeApiService) { }

  @Input()
  set nickname(v: string) {
    if (v)
      this.url = this.api.toPictureUrl(v);
    else
      this.url = "";
  }

  @Input() token = 0;
}
