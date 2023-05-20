import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { authRoutes } from './app-routing.module';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'Sype!';

  get playing(): boolean {
    return sessionStorage.getItem('playing') == 'true';
  }

  constructor(private router: Router) {
    router.events.subscribe(() => {
      if (router.url != '/play')
      sessionStorage.setItem('playing', String(false));
    });
  }

  getRoute(): string {
    return this.router.url.split('/')[1];
  }

  authorized(): boolean {
    return this.router.config == authRoutes;
  }
}
