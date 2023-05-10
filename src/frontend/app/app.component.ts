import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { authRoutes } from './app-routing.module';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'Sype!';

  constructor(private router: Router) {}

  getRoute(): string {
    return this.router.url.split('/')[1];
  }

  authorized(): boolean {
    return this.router.config == authRoutes;
  }
}
