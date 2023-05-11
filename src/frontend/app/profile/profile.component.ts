import { Component, ElementRef, ViewChild } from '@angular/core';
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
  @ViewChild('nicknameSpan') nicknameSpan!: ElementRef<HTMLSpanElement>;
  private _nickname!: string;
  prevEditingNickname!: string;
  isMine = false;
  shakeNickname = false;
  preventNextNicknameInputEvent = false;

  set nickname(v: string) {
    this.prevEditingNickname = v;
    this._nickname = v;
  }

  get nickname(): string {
    return this._nickname;
  }

  constructor(
    public api: SypeApiService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    if ('id' in route.snapshot.params)
      this.nickname = this.route.snapshot.params['id'];
    else
    {
      api.getLogin().pipe(catchError(err => {
        this.router.config = defaultRoutes;
        this.router.navigateByUrl('/');
        return '';
      })).subscribe(data => {
        if (typeof data != 'string')
        {
          this.nickname = data.nickname;
          this.isMine = true;
        }
      });
    }
  }

  nicknameInput() {
    if (this.preventNextNicknameInputEvent)
    {
      this.preventNextNicknameInputEvent = false;
      return;
    }

    let newNickname = this.nicknameSpan.nativeElement.textContent ?? '';

    if (
      !newNickname ||
      newNickname.trim() != newNickname ||
      newNickname.length > 20 ||
      newNickname.match(/[ \t\n\r\0\x0B]/)
    ) {
      this.nicknameSpan.nativeElement.textContent = this.prevEditingNickname;
      this.shakeNickname = false;
      setTimeout(() => this.shakeNickname = true, 100)
    }
    else
      this.prevEditingNickname = newNickname;
  }
}
