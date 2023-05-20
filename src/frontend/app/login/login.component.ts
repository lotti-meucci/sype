import { HttpErrorResponse } from '@angular/common/http';
import { Component, ViewChild, ElementRef } from '@angular/core';
import { Router } from '@angular/router';
import { authRoutes } from 'app/app-routing.module';
import { ErrorResponse } from 'app/types/error-response';
import { SypeApiService } from 'app/services/sype-api.service';
import { catchError } from 'rxjs';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  @ViewChild('nickname') nickname!: ElementRef<HTMLInputElement>;
  @ViewChild('password') password!: ElementRef<HTMLInputElement>;
  error?: string;
  shake: boolean = false;
  signedUp: boolean = false;
  hidden: boolean = true;
  redirect: string;

  constructor(
    private api: SypeApiService,
    private router: Router
  ) {
    this.api.getLogin().pipe(catchError(() => {
      this.hidden = false;
      return '';
    })).subscribe(data => this.authorize());

    const primary = this.router.getCurrentNavigation()?.initialUrl.root.children['primary'];
    this.redirect = primary ? primary.segments.map(s => '/' + s.path).join('') : '/'
    this.signedUp = this.router.getCurrentNavigation()?.extras.state?.['signedUp'];
  }

  signingUp() {
    return this.router.url == "/signup";
  }

  authorize() {
    this.router.config = authRoutes;
    this.router.navigateByUrl(this.redirect);
  }

  submit() {
    if (this.signingUp()) {
      this.api.putUser({
        nickname: this.nickname.nativeElement.value,
        password: this.password.nativeElement.value
      }).pipe(catchError((err: HttpErrorResponse) => {
        const body = err.error as ErrorResponse;
        this.shake = false;
        this.error = body?.message;
        setTimeout(() => this.shake = true, 100);
        return '';
      })).subscribe(data => {
        this.router.navigateByUrl('/', { state: { signedUp: true } });
      })

      return false;
    }

    this.api.postLogin({
      nickname: this.nickname.nativeElement.value,
      password: this.password.nativeElement.value
    }).pipe(catchError(() => {
      this.shake = false;
      this.error = '"nickname" or "password" are not valid.';
      setTimeout(() => this.shake = true, 100);
      return '';
    })).subscribe(data => {
      this.authorize();
    })

    return false;
  }
}
