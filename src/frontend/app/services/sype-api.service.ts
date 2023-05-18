import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, isDevMode } from '@angular/core';
import { NicknameResponse } from 'app/interfaces/nickname-response';
import { CredentialsRequest } from 'app/interfaces/credentials-request';
import { Observable } from 'rxjs';
import { Difficulty } from 'app/interfaces/difficulty';
import { TextResponse } from 'app/interfaces/text-response';
import { DifficultyRequest } from 'app/interfaces/difficulty-request';

const CONFIG = { withCredentials: true };

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
    return `${this.prefix}/pictures.php?user=${nickaname}`;
  }

  getDifficulties(): Observable<Difficulty[]> {
    return this.http.get<Difficulty[]>(this.prefix + '/difficulties.php', CONFIG);
  }

  postStartGame(difficulty: DifficultyRequest): Observable<TextResponse> {
    return this.http.post<TextResponse>(this.prefix + '/startGame.php', difficulty, CONFIG);
  }
}
