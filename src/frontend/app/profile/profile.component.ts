import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { defaultRoutes } from 'app/app-routing.module';
import { SypeApiService } from 'app/services/sype-api.service';
import { catchError } from 'rxjs';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent {
  pictureUrl = "";
  _nickname = "";

  get nickname(): string {
    return this._nickname
  }

  set nickname(v : string) {
    this.pictureUrl = this.api.toPictureUrl(v);
    this._nickname = v;
  }

  constructor(
    public api: SypeApiService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    if ('id' in route.snapshot.params)
      this.nickname = route.snapshot.params['id'];
    else
    {
      api.getLogin().pipe(catchError(err => {
        router.config = defaultRoutes;
        router.navigateByUrl('/');
        return '';
      })).subscribe(data => {
        if (typeof data != 'string')
          this.nickname = data.nickname;
      });
    }
  }

  onPictureError() {
    this.pictureUrl = '/assets/profile_default.jpg';
  }
}
