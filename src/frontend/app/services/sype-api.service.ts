import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, isDevMode } from '@angular/core';
import { NicknameResponse } from 'app/types/nickname-response';
import { CredentialsRequest } from 'app/types/credentials-request';
import { Observable, map } from 'rxjs';
import { Difficulty } from 'app/types/difficulty';
import { TextResponse } from 'app/types/text-response';
import { DifficultyRequest } from 'app/types/difficulty-request';
import { GameResultRequest } from 'app/types/game-result';
import { Game } from 'app/types/game';
import { ErrorsNumberResponse } from 'app/types/errors-number-response';

const CONFIG = { withCredentials: true };

const colors = [
  "success",
  "warning",
  "danger",
]

@Injectable({
  providedIn: 'root'
})
export class SypeApiService {
  prefix = "";

  constructor(protected http: HttpClient) {
    if (isDevMode())
      this.prefix = "/proxy";
  }

  getLogin(): Observable<NicknameResponse> {
    return this.http.get<NicknameResponse>(this.prefix + '/login.php', CONFIG);
  }

  postLogin(credentials: CredentialsRequest): Observable<unknown> {
    return this.http.post(this.prefix + '/login.php', credentials, CONFIG);
  }

  postLogout(): Observable<unknown> {
    return this.http.post(this.prefix + '/logout.php', CONFIG);
  }

  putUser(credentials: CredentialsRequest): Observable<unknown> {
    return this.http.put(this.prefix + '/users.php', credentials, CONFIG);
  }

  patchUser(user: string, credentials: CredentialsRequest): Observable<unknown> {
    return this.http.patch(
      this.prefix + '/users.php',
      credentials,
      { ...CONFIG, params: new HttpParams().set('user', user) }
    );
  }

  deleteUser(user: string): Observable<unknown> {
    return this.http.delete(
      this.prefix + '/users.php',
      { ...CONFIG, params: new HttpParams().set('user', user) }
    );
  }

  putPicture(user: string, png: Blob): Observable<unknown> {
    return this.http.put(
      this.prefix + '/pictures.php',
      png,
      { ...CONFIG, params: new HttpParams().set('user', user) }
    );
  }

  patchPicture(user: string, png: Blob): Observable<unknown> {
    return this.http.patch(
      this.prefix + '/pictures.php',
      png,
      { ...CONFIG, params: new HttpParams().set('user', user) }
    );
  }

  toPictureUrl(nickaname: string) {
    return `${this.prefix}/pictures.php?user=${encodeURIComponent(nickaname)}`;
  }

  getDifficulties(): Observable<Difficulty[]> {
    return this.http.get<Difficulty[]>(this.prefix + '/difficulties.php', CONFIG).pipe(
      map((difficulties) => {
        let i = 0;

        for (const difficulty of difficulties) {
          if (i >= colors.length)
            i = 0;

          difficulty.color = colors[i++];
        }

        return difficulties;
      })
    );
  }

  postStartGame(difficulty: DifficultyRequest): Observable<TextResponse> {
    return this.http.post<TextResponse>(this.prefix + '/startGame.php', difficulty, CONFIG);
  }

  postEndGame(gameResult: GameResultRequest): Observable<ErrorsNumberResponse> {
    return this.http.post<ErrorsNumberResponse>(this.prefix + '/endGame.php', gameResult, CONFIG);
  }

  getGames(user: string): Observable<Game[]> {
    return this.http.get<Game[]>(
      this.prefix + '/games.php',
      { ...CONFIG, params: new HttpParams().set('user', user) }
    );
  }
}
