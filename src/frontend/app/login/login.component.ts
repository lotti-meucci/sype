import { HttpErrorResponse } from '@angular/common/http';
import { Component, ViewChild, ElementRef } from '@angular/core';
import { Router } from '@angular/router';
import { authRoutes } from 'app/app-routing.module';
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

  constructor(
    private api: SypeApiService,
    private router: Router
  ) {
    this.api.getLogin().subscribe(data => this.authorize());
  }

  signingUp() {
    return this.router.url == "/signup";
  }

  authorize() {
    this.router.config = authRoutes;
    this.router.navigateByUrl('/play');
  }

  submit() {
    console.log({
      nickname: this.nickname.nativeElement.value,
      password: this.password.nativeElement.value
    });
    this.api.postLogin({
      nickname: this.nickname.nativeElement.value,
      password: this.password.nativeElement.value
    }).pipe(catchError(err => {
      this.error = 'Nickname or password are not valid.'
      return '';
    })).subscribe(data => {
      this.authorize();
    })

    return false;
  }
}
