import { TextResponse } from "./text-response";

export interface GameResultRequest extends TextResponse {
  result: number;
}
