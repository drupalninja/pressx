export interface Post {
  id: string;
  title: string;
  date: string;
  excerpt: string;
}

export interface PostsResponse {
  posts: {
    nodes: Post[];
  };
}
