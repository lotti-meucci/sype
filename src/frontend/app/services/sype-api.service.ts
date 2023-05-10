import { HttpClient } from '@angular/common/http';
import { Injectable, isDevMode } from '@angular/core';
import { NicknameResponse } from 'app/interfaces/nickname-response';
import { CredentialsRequest } from 'app/interfaces/credentials-request';
import { Observable } from 'rxjs';

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

  putUser(credentials: CredentialsRequest): Observable<unknown> {
    return this.http.put(this.prefix + '/users.php', credentials, CONFIG);
  }

  toPictureUrl(nickaname: string) {
    return `${this.prefix}/pictures.php?user=${nickaname}`;
  }
}
