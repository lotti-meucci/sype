import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LogoComponent } from './logo/logo.component';
import { NavbarComponent } from './navbar/navbar.component';
import { GameComponent } from './game/game.component';
import { RankingsComponent } from './rankings/rankings.component';
import { ProfileComponent } from './profile/profile.component';
import { LoginComponent } from './login/login.component';
import { ProfilePictureComponent } from './profile-picture/profile-picture.component';
import { GameResultComponent } from './game-result/game-result.component';

@NgModule({
  declarations: [
    AppComponent,
    LogoComponent,
    NavbarComponent,
    GameComponent,
    RankingsComponent,
    ProfileComponent,
    LoginComponent,
    ProfilePictureComponent,
    GameResultComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
