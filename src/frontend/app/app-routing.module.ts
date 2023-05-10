import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { GameComponent } from './game/game.component';
import { ProfileComponent } from './profile/profile.component';
import { RankingsComponent } from './rankings/rankings.component';

export const authRoutes: Routes = [
  { path: 'play', component: GameComponent },
  { path: 'rankings', component: RankingsComponent },
  { path: 'profile', component: ProfileComponent },
  { path: 'profile/:id', component: ProfileComponent },
  { path: '**', redirectTo: '/play' }
]

export const defaultRoutes: Routes = [
  { path: '', component: LoginComponent },
  { path: 'signup', component: LoginComponent },
  { path: '**', redirectTo: '/' }
];

@NgModule({
  imports: [RouterModule.forRoot(defaultRoutes, { onSameUrlNavigation: 'reload' })],
  exports: [RouterModule]
})
export class AppRoutingModule { }
