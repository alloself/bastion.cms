declare module 'baguettebox.js' {
  function run(
    selector: string,
    options?: {
      [key: string]: any;
    }
  ): void;
  
  export default {
    run
  };
} 