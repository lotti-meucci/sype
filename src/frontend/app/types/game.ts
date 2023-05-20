import { DifficultyRequest } from "./difficulty-request";
import { ErrorsNumberResponse } from "./errors-number-response";

export interface Game extends ErrorsNumberResponse {
  nickname: string,
  datetime: Date,
  result: number,
  difficulty?: number,
}
