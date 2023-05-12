import { Component, ElementRef, NgZone, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { defaultRoutes } from 'app/app-routing.module';
import { ErrorResponse } from 'app/interfaces/error-response';
import { SypeApiService } from 'app/services/sype-api.service';
import { catchError } from 'rxjs';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent {
  @ViewChild('nicknameSpan') nicknameSpan!: ElementRef<HTMLSpanElement>;
  @ViewChild('pictureFileInput') pictureFileInput?: ElementRef<HTMLInputElement>;
  @ViewChild('pictureCanvas') pictureCanvas?: ElementRef<HTMLCanvasElement>;
  private _nickname!: string;
  prevEditingNickname!: string;
  isMine = false;
  randomToken = 0;
  shakeNickname = false;
  showingNicknameError = false;
  nicknameErrorMessage = '';

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
    private route: ActivatedRoute,
    private ngZone: NgZone
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
    const containsBR = this.nicknameSpan.nativeElement.firstElementChild instanceof HTMLBRElement;

    // Copies the text from the span (without HTML code).
    const newNickname = this.nicknameSpan.nativeElement.textContent ?? '';

    // Overrides the span element content with just text (removes HTML elements).
    if (this.nicknameSpan.nativeElement.children.length > 0)
      this.nicknameSpan.nativeElement.textContent = newNickname;

    if (
      !newNickname ||
      newNickname.trim() != newNickname ||
      newNickname.length > 20 ||
      newNickname.match(/[ \t\n\r\0\x0B]/)
    ) {
      this.nicknameSpan.nativeElement.textContent = this.prevEditingNickname;
      this.startShakeNickname();
    }
    else
      this.prevEditingNickname = newNickname;

    if (containsBR)
    {
      this.nicknameSpan.nativeElement.blur();
      return;
    }
  }

  nicknameBlur() {
    const newNickname = this.nicknameSpan.nativeElement.textContent ?? '';

    if (this.nickname == newNickname)
      return;

    this.api.patchUser(this.nickname, { nickname: newNickname }).pipe(catchError(err => {
      const body = err.error as ErrorResponse;
      this.showNicknameError(body?.message);
      this.nicknameSpan.nativeElement.textContent = this.nickname;
      this.startShakeNickname();
      return '';
    })).subscribe(data => this.nickname = newNickname);
  }

  startShakeNickname() {
    this.shakeNickname = false;
    setTimeout(() => this.shakeNickname = true, 100)
  }

  showNicknameError(message: string) {
    this.nicknameErrorMessage = message;
    this.showingNicknameError = true;
    setTimeout(() => this.showingNicknameError = false, 5000);
  }

  onPictureSelected() {
    if (!this.pictureFileInput || !this.pictureCanvas)
      return;

    const canvas = this.pictureCanvas.nativeElement;
    const fileReader = new FileReader();

    fileReader.onload = () => {
      const image = new Image();

      image.onload = () => {
        const context = canvas.getContext('2d')!;
        context.clearRect(0, 0, canvas.width, canvas.height);
        canvas.width = image.width;
        canvas.height = image.height;
        context.drawImage(image, 0, 0);

        canvas.toBlob(blob => {
          this.api.patchPicture(this.nickname, blob!).subscribe(data => {
            location.reload();
          })
        })
      }

      image.src = fileReader.result as string;
    }

    const file = this.pictureFileInput.nativeElement.files![0];
    fileReader.readAsDataURL(file);
  }
}
